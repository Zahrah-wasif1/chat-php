const { Server } = require("socket.io");
const axios = require("axios");
require("dotenv").config();

// Get configuration from environment variables
const PORT = process.env.SOCKET_PORT || 3000;
const API_URL = process.env.API_URL || process.env.APP_URL || "http://localhost";

const io = new Server(PORT, {
  cors: {
    origin: "*",
  }
});

console.log(`Socket server running on port ${PORT}`);
console.log(`API URL: ${API_URL}`);

io.on("connection", (socket) => {
  console.log("User connected:", socket.id);

  socket.on("join_room", (data) => {
    socket.join(data.room_id);
    console.log(`User ${data.user_id} joined room ${data.room_id}`);
  });

  socket.on("send_message", async (data) => {
    try {
      // Save message via PHP REST API
      const apiEndpoint = `${API_URL}/api/chat/messages`;
      const response = await axios.post(apiEndpoint, data, {
        headers: {
          'Content-Type': 'application/json',
          'Authorization': data.token || ''
        }
      });
      const savedMessage = response.data;

      // Broadcast to room
      io.to(data.room_id).emit("receive_message", savedMessage);
    } catch (error) {
      console.error("Error saving message:", error.message);
      socket.emit("error", { message: "Failed to save message" });
    }
  });

  socket.on("typing_start", (data) => {
    socket.to(data.room_id).emit("typing_start", data);
  });

  socket.on("disconnect", () => {
    console.log("User disconnected:", socket.id);
  });
});
