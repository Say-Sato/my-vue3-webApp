import express from 'express'
import mysql from 'mysql'
import cors from 'cors'

const app = express()
app.use(cors())

// ■■■ Important Notice ■■■
// This is an example server file.
// To run the server in your local environment, copy this file to `server.js`
// and fill in the correct database connection information below.
// It is recommended to use environment variables to securely manage your credentials.
//
// Example using environment variables:
//
// import dotenv from 'dotenv';
// dotenv.config();
//
// const db = mysql.createConnection({
//   host: process.env.DB_HOST,
//   user: process.env.DB_USER,
//   password: process.env.DB_PASSWORD,
//   database: process.env.DB_DATABASE,
// });

const db = mysql.createConnection({
  host: 'your_database_host',
  user: 'your_database_user',
  password: 'your_database_password',
  database: 'your_database_name',
})

// Connect to MySQL
db.connect(err => {
  if (err) {
    console.error('MySQL connection error: ' + err.stack)
    console.error('Please make sure you have configured the database connection information correctly in `server.js`.');
    return
  }
  console.log('Connected to MySQL as ID ' + db.threadId)
})

// Endpoint to get profile data
app.get('/api/profiles', (req, res) => {
  db.query('SELECT * FROM profile', (error, results) => {
    if (error) {
      return res.status(500).json({ error: error.message })
    }
    res.json(results) // Return results in JSON format
  })
})

// Start the server
const PORT = process.env.PORT || 3000
app.listen(PORT, () => {
  console.log(`Server is running on port ${PORT}`)
})
