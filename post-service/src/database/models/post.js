const mongoose = require("mongoose");
const { randomUUID } = require("crypto");

const postSchema = new mongoose.Schema({
  _id: {
    type: String,
    default: randomUUID,
  },
  caption: {
    type: String,
    required: false,
  },
  status: {
    type: String,
    enum: ["showing", "await", "deleted", "hidden"],
    default: "await",
  },
  author: {
    type: String,
    ref: "user",
  },
  createdAt: {
    type: Number,
    default: () => Math.floor(Date.now() / 1000),
  },
  updatedAt: {
    type: Number,
    default: () => Math.floor(Date.now() / 1000),
  },
});

module.exports = mongoose.model("post", postSchema);
