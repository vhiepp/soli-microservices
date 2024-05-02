class PostRepository {
  async getPosts(page) {
    const perPage = 8;
    const skip = (page - 1) * perPage;

    // Retrieve posts from the database with pagination
    const posts = await PostModel.find().populate("author").skip(skip).limit(perPage);
    return posts;
  }
}

module.exports = PostRepository;
