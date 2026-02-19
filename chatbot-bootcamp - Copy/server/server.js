const express = require("express");
const mysql = require("mysql2/promise");
const cors = require("cors");
const dotenv = require("dotenv");
dotenv.config();
const app = express();
app.use(cors());
app.use(express.json());
const pool = mysql.createPool({
host: "localhost",
user: "root",
password: "",
database: "college_chatbot",
waitForConnections: true,
connectionLimit: 10,
queueLimit: 0,
});
app.post("/api/chat", async (req, res) => {
    const { message, username } = req.body;
    let response="Sorry I didn't understand that. Please try asking about routine or upcoming events";
    try {
        if (
        message.toLowerCase().includes("schedule") ||
        message.toLowerCase().includes("routine")
        ) {
            query = `
            SELECT r.day, r.subject, r.start_time, r.end_time, r.room, r.instructor
            FROM routines r
            JOIN users u ON r.semester = u.semester
            WHERE u.username = ?
            ORDER BY r.day, r.start_time
            `;
            params = [username];
            const [rows] = await pool.execute(query, params);
            if (rows.length > 0) {
                response = "<b>Here's your schedule:</b><ul type=\"disc\">";
                rows.forEach((row) => {
                    response += `<li>${row.day}: ${row.subject} from ${row.start_time} to `+
                    `${row.end_time} in room ${row.room} with ${row.instructor}</li>`;
                });
                response+="</ul>"
            } else {
                response = "No schedule found for your semester.";
            }
        }else if (
        message.toLowerCase().includes("event") ||
        message.toLowerCase().includes("events")
        ) {
            query = "SELECT event_name, event_date, location, description FROM events ORDER BY event_date";
            const [rows] = await pool.execute(query);
            if (rows.length > 0) {
                response = "<table className=\"b-1\"><caption>Upcoming Events</caption><tr><th>Name</th><th>Event Date</th><th>Description</th></tr>";
                rows.forEach((row) => {
                    response += `<tr><td>${row.event_name}</td><td>${row.event_date}</td><td>${row.location}</td><td>${row.description}</td></tr>`;
                });
                response+="</table>";
            } else {
                response = "No upcoming events found.";
            }
        }
        res.json({ reply: response });
    } catch (error) {
        console.error("Error processing query:", error);
        res.status(500).json({
            reply: "An error occurred while processing your request."
        });
    }
});
const PORT = process.env.PORT || 5000;
app.listen(PORT, () => console.log(`Server running on port ${PORT}`));