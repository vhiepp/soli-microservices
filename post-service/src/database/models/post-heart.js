const mongoose = require("mongoose");

const mediaSchema = new mongoose.Schema(
  {
    postId: {
      type: String,
      required: true,
      ref: "post",
    },
    user: {
      type: String,
      required: true,
      ref: "user",
    },
    active: {
      type: Boolean,
      default: true,
    },
    createdAt: {
      type: Number,
      default: () => Math.floor(Date.now() / 1000),
    },
    updatedAt: {
      type: Number,
      default: () => Math.floor(Date.now() / 1000),
    },
  },
  { _id: false }
);

module.exports = mongoose.model("post-heart", mediaSchema);
