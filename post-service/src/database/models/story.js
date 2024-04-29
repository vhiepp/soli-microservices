const mongoose = require("mongoose");
const { randomUUID } = require("crypto");

const mediaSchema = new mongoose.Schema({
  _id: {
    type: String,
    default: randomUUID,
  },
  content: {
    type: String,
    required: false,
  },
  status: {
    type: String,
    enum: ["showing", "await", "deleted", "hidden"],
    default: "await",
  },
  fileUrl: {
    type: String,
    required: true,
    length: 255,
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

module.exports = mongoose.model("story", mediaSchema);
