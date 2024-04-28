const mongoose = require("mongoose");
const { randomUUID } = require("crypto");

const Schema = mongoose.Schema;

const UserSchema = new Schema({
  _id: {
    type: String,
    default: randomUUID,
  },
  fullname: {
    type: String,
    required: true,
  },
  // firstname: {
  //   type: String,
  //   required: true,
  // },
  // lastname: {
  //   type: String,
  //   required: true,
  // },
  // uid: {
  //   type: String,
  //   required: true,
  //   unique: true,
  // },
  createdAt: {
    type: Number,
    default: () => Math.floor(Date.now() / 1000),
  },
  updatedAt: {
    type: Number,
    default: () => Math.floor(Date.now() / 1000),
  },
});

module.exports = mongoose.model("user", UserSchema);
