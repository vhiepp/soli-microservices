const express = require("express");
const { PORT } = require("./config");
const { databaseConnection } = require("./database");
const expressApp = require("./express-app");
const { PostModel, UserModel } = require("./database/models");

const StartServer = async () => {
  const app = express();

  await databaseConnection();

  await expressApp(app);

  // UserModel.create({ fullname: "User 1" });

  // const post = new PostModel({ title: "Post 1", author: "d73ce113-9049-4865-94bd-4e9e746e7190" });
  // post.save();

  // const post = await PostModel.find().populate("author");
  // console.log(post);

  app
    .listen(PORT || 8001, () => {
      console.log(`listening to port ${PORT}`);
    })
    .on("error", (err) => {
      console.log(err);
      process.exit();
    });
};

StartServer();
