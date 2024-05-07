const { PostModel } = require("../models");

class PostRepository {
  async getPosts(page) {
    const perPage = 8;
    const skip = (page - 1) * perPage;

    // Retrieve posts from the database with pagination
    const posts = await PostModel.find().populate("author").skip(skip).limit(perPage);
    return posts;
  }

  async findById(id) {
    // Find a post by its ID
    const post = await PostModel.findById(id).populate("author");
    return post;
  }

  async findByAuthorId(authorId, page) {
    const perPage = 8;
    const skip = (page - 1) * perPage;

    // Find posts by author ID with pagination
    const posts = await PostModel.find({ author: authorId }).skip(skip).limit(perPage);
    return posts;
  }

  async createPost(postData) {
    // Create a new post using the provided data
    const post = await PostModel.create(postData);
    return post;
  }

  async updatePost(id, postData) {
    // Find the post by its ID and update it with the provided data
    const updatedPost = await PostModel.findByIdAndUpdate(id, postData, { new: true }).populate("author");
    return updatedPost;
  }
}

module.exports = PostRepository;
