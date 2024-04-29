const mongoose = require("mongoose");
const { randomUUID } = require("crypto");

const Schema = mongoose.Schema;

const userSchema = new Schema({
  _id: {
    type: String,
    default: randomUUID,
  },
  fullname: {
    type: String,
    required: true,
    length: 100,
  },
  firstname: {
    type: String,
    required: true,
    length: 50,
  },
  lastname: {
    type: String,
    required: true,
    length: 50,
  },
  uid: {
    type: String,
    required: true,
    unique: true,
    length: 100,
  },
  avatarUrl: {
    type: String,
    default: "",
    length: 255,
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

module.exports = mongoose.model("user", userSchema);
