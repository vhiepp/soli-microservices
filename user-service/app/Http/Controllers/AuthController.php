<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\UserService;
use App\Models\Account;
use App\Models\User;
use Hash;
use App\Jobs\UserCreatedJob;

class AuthController extends Controller
{
    protected UserService $userService;
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->userService = new UserService();
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function signInWithEmailPassword(Request $request)
    {
            $account = Account::whereProvider([
                'provider' => 'email/password',
                'provider_id' => $request->email,
                'username' => $request->email,
            ])->first();
            if ($account && Hash::check($request->password, $account->password)) {

                $user = $account->user;
                $user->currentAvatar;

                $token = auth()->claims(['user' => $user->toArray()])->login($user);
                if ($token) {
                    $cookie = cookie('token', $token, auth()->factory()->getTTL());
                    return response()->json(reshelper()->withFormat($this->resProfile($user, $token)))->cookie($cookie);
                }
            }
        try {
        } catch (Exception $exception) { }
        return response()->json(reshelper()->withFormat(null, 'Error, could be due to wrong email or password', 'error', false, true));
    }

    public function signInWithFirebase(Request $request)
    {
        try {
            $decoded_payload = false;
            if (gettype($request->firebase_access_token) == 'string') {
                $token = explode('.', $request->firebase_access_token);
                $encoded_payload = $token[1];
                $decoded_payload = base64_decode($encoded_payload, true);
            }
            if ($decoded_payload) {
                $payload = json_decode($decoded_payload, true);
            } else {
                $firebaseInfo = $request->firebase_access_token;
                $nameTemp = explode('@', $firebaseInfo['providerData'][0]['email']??$firebaseInfo['email']??"user@")[0];
                $payload = [
                    "name" => $firebaseInfo['providerData'][0]['displayName']??$firebaseInfo['displayName']??$nameTemp,
                    "picture" => $firebaseInfo['providerData'][0]['photoURL']??$firebaseInfo['photoURL']??null,
                    "iss" => "",
                    "aud" => "sv5t-tvu-ca74b",
                    "auth_time" => null,
                    "user_id" => "",
                    "sub" => "",
                    "iat" => "",
                    "exp" => $firebaseInfo['stsTokenManager']['expirationTime']??time()+60,
                    "email" => $firebaseInfo['providerData'][0]['email']??$firebaseInfo['email'],
                    "email_verified" => true,
                    "firebase" => [
                        "identities" => [
                            $firebaseInfo['providerData'][0]['providerId'] => [
                                $firebaseInfo['providerData'][0]['uid']
                            ],
                            "email" => [$firebaseInfo['providerData'][0]['email']??$firebaseInfo['email']],
                        ],
                        "sign_in_provider" => $firebaseInfo['providerData'][0]['providerId']
                    ]
                ];
            }

            if ((env('APP_ENV') == 'production' && $payload['exp'] >= time()) || env('APP_ENV') == 'local') {
                $provider = $payload['firebase']['sign_in_provider'];
                $providerId = $payload['firebase']['identities'][$provider][0];
                $account = Account::whereProvider([
                    'provider' => $provider,
                    'provider_id' => $providerId,
                    'username' => $provider . '-' . $payload['email']
                ])->first();
                if ($account) {
                    $user = $account->user;
                } else {
                    $user = User::where('email', $payload['email'])->first();
                    if ($user == null) {
                        $user = User::create([
                            'full_name' => $payload['name'],
                            'firstname' => $payload['name'],
                            'lastname' => '',
                            'email' => $payload['email']
                        ]);
                        try {
                            if (str($payload['picture'])->isUrl()) {
                                $this->userService->changeAvatar($user, $payload['picture']);
                            }
                        } catch (\Exception $exception) {}
                    }
                    $user->accounts()->create([
                        'username' => $provider . '-' . $payload['email'],
                        'password' => rand() . env('PASSWORD_KEY', '.') . rand(),
                        'provider' => $provider,
                        'provider_id' => $providerId
                    ]);
                }
            }
            $user = User::find($user->id);
            $token = auth()->claims(['user' => $user->toArray()])->login($user);
            if (!$token) {
                return response()->json(reshelper()->withFormat(null, 'Unauthorized', 'error', false, true));
            }
            $cookie = cookie('token', $token, auth()->factory()->getTTL());
            return response()->json(reshelper()->withFormat($this->resProfile($user), 'Successfully sign in'))->cookie($cookie);
        } catch (\Exception $exception) {}

        return response()->json(reshelper()->withFormat(null, 'It could be due to expired firebase_access_token or input parameter error', 'error', false, true));
    }

    public function signInWithOAuth(Request $request)
    {
        // try {
            $createNewUser = false;
            $provider = $request->input('sign_in_provider');
            $providerId = $request->input('provider_id');
            $account = Account::whereProvider([
                'provider' => $provider,
                'provider_id' => $providerId,
                'username' => $provider . '-' . $request->input('email')
            ])->first();
            if ($account) {
                $user = $account->user;
            } else {
                $user = User::where('email', $request->input('email'))->first();
                if ($user == null) {
                    $user = User::create([
                        'fullname' => $request->input('fullname'),
                        'firstname' => $request->input('firstname'),
                        'lastname' => $request->input('lastname'),
                        'email' => $request->input('email')
                    ]);
                    $createNewUser = true;
                    try {
                        if (str($request->input('avatar_url'))->isUrl()) {
                            $this->userService->changeAvatar($user, $request->input('avatar_url'));
                        }
                    } catch (\Exception $exception) {}
                }
                $user->accounts()->create([
                    'username' => $provider . '-' . $request->input('email'),
                    'password' => rand() . env('PASSWORD_KEY', '.') . rand(),
                    'provider' => $provider,
                    'provider_id' => $providerId
                ]);
            }
            $user = User::find($user->id);
            $user->currentAvatar;
            if ($createNewUser) {
                UserCreatedJob::dispatch($user->toArray());
            }
            $token = auth()->claims(['user' => $user->toArray()])->login($user);
            if (!$token) {
                return response()->json(reshelper()->withFormat(null, 'Unauthorized', 'error', false, true));
            }
            $cookie = cookie('token', $token, auth()->factory()->getTTL());
            return response()->json(reshelper()->withFormat($this->resProfile($user, $token), 'Successfully sign in'))->cookie($cookie);
        // } catch (\Exception $exception) {}

        // return response()->json(reshelper()->withFormat(null, 'It could be due to expired firebase_access_token or input parameter error', 'error', false, true));
    }
    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function profile()
    {

        $user = auth()->user();
        $user->currentAvatar;
        $token = auth()->claims(['user' => $user->toArray()])->login($user);
        $cookie = cookie('token', $token, auth()->factory()->getTTL());
        return response()->json(reshelper()->withFormat($this->resProfile($user, $token)))->cookie($cookie);
    }

    public function resProfile($user, $token = null)
    {
        return [
            'profile' => $user,
            'access_token' => $token
        ];
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function signOut()
    {
        $cookie = cookie()->forget('token');
        return response()->json(reshelper()->withFormat(null, 'Successfully sign out', 'success', true, false))->cookie($cookie);
    }
}
