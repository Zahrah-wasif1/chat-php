const { Server } = require("socket.io");
const axios = require("axios");

const io = new Server(3000, {
  cors: {
    origin: "*",
  }
});

io.on("connection", (socket) => {
  console.log("User connected:", socket.id);

  socket.on("join_room", (data) => {
    socket.join(data.room_id);
    console.log(`User ${data.user_id} joined room ${data.room_id}`);
  });

  socket.on("send_message", async (data) => {
    // Save message via PHP REST API
    const response = await axios.post("http://localhost/backend/public/save_message.php", data);
    const savedMessage = response.data;

    // Broadcast to room
    io.to(data.room_id).emit("receive_message", savedMessage);
  });

  socket.on("typing_start", (data) => {
    socket.to(data.room_id).emit("typing_start", data);
  });

  socket.on("disconnect", () => {
    console.log("User disconnected:", socket.id);
  });
});
