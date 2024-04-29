const mongoose = require("mongoose");
const { randomUUID } = require("crypto");

const mediaSchema = new mongoose.Schema({
  _id: {
    type: String,
    default: randomUUID,
  },
  fileUrl: {
    type: String,
    required: true,
    length: 255,
  },
  type: {
    type: String,
    enum: ["image", "video"],
    required: true,
  },
  postId: {
    type: String,
    ref: "post",
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

module.exports = mongoose.model("media", mediaSchema);
