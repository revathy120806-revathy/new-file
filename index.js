import express from 'express'
import path from 'path'
import { fileURLToPath } from 'url';
import http from 'http'
import https from 'https'
import fs from 'fs'
import axios from 'axios'
import { MongoClient } from 'mongodb';
import cors from 'cors'
import fetch from 'node-fetch';



const app=express()

app.use(cors())

app.use(express.urlencoded({extended:true}));

const dbName="School"
const url="mongodb://localhost:27017"
 const client=new MongoClient(url);

client.connect().then((connection)=>{
const db=connection.db(dbName);

app.get("/contact.html",async (req,resp)=>{
const collection=db.collection('department')
const  data=await collection.find().toArray()
const absPath=path.resolve('contact.html')
resp.sendFile(absPath)


})

app.post("/Datasheet",async(req,resp)=>{
const name=req.body.name
const email=req.body.email
const subject=req.body.subject
const message=req.body.message
const collection=db.collection('department')
const result=await collection.insertOne({name,email,subject,message})   
resp.redirect('/contact.html')
})




})



/* async function dbConnection(){
await client.connect()

const db= client.db(dbName)
const collection= db.collection('department')
const result=await collection.find().toArray()

 console.log(result)

 }



dbConnection()*/








const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);


app.set("view engine","ejs")
const pathName=path.resolve('assets')
const imgPath=path.resolve(__dirname)
const folderName='img'
//app.use(express.static(path.join(pathName,folderName)))
app.use('/node_modules', express.static(path.join(__dirname, 'node_modules')))


app.get("/",(req,resp)=>{
 const absPath=path.resolve('index.html')
resp.sendFile(absPath)
console.log(absPath)

app.use(express.static(imgPath))
console.log(imgPath)



})


app.get("/contact.html",(req,resp)=>{
const absPath=path.resolve('contact.html')
resp.sendFile(absPath)

})


app.get("/resume.html",(req,resp)=>{
const absPath=path.resolve('resume.html')
resp.sendFile(absPath)


})


app.get("/about.html",(req,resp)=>{
const absPath=path.resolve('about.html')
resp.sendFile(absPath)

})

app.get("/index.html",(req,resp)=>{
const absPath=path.resolve('index.html')
resp.sendFile(absPath)


})

app.get('/download', async (req, res) => {
    const fileUrl = 'https://1drv.ms/b/c/c365a2806ea20738/IQDeFD_SWbDjRKszsEuzCmTVAatFek3NxlkKv4MoccJSTms?e=VoJIck'; // Replace with your PDF URL
    const localPath = path.join(__dirname, 'downloaded.pdf');
console.log(localPath)
    try {
        // Fetch PDF with headers to avoid 403
        const response = await axios.get(fileUrl, {
            responseType: 'stream',
            headers: {
                'User-Agent': 'Norton Private Browser/ 142.0.33025.177', // Some servers require this
                // 'Authorization': 'Bearer YOUR_TOKEN', // If needed
                // 'Cookie': 'session=abc123', // If needed
            }
        });

        // Save PDF locally
        const writer = fs.createWriteStream(localPath);
        response.data.pipe(writer);

        writer.on('finish', () => {
            // Send file to client
            res.download(localPath, 'myfile.pdf', (err) => {
                if (err) {
                    console.error('Error sending file:', err);
                    res.status(500).send('Error sending file');
                }
            });
        });

        writer.on('error', (err) => {
            console.error('Error writing file:', err);
            res.status(500).send('Error saving file');
        });

    } catch (error) {
        console.error('Download failed:', error.message);
        res.status(403).send('Failed to fetch PDF. Check URL or headers.');
    }
});






const GITHUB_TOKEN = process.env.GITHUB_TOKEN; // Store token in env variable
const OWNER = 'revaty120806-revathy';
const REPO = 'new-file';

async function createIssue(title, body) {
  try {
    const response = await fetch(`https://api.github.com/repos/${OWNER}/${REPO}/issues`, {
      method: 'POST',
      headers: {
        'Authorization': `token ${GITHUB_TOKEN}`, // or `Bearer` for fine-grained tokens
        'Accept': 'application/vnd.github+json',
        'User-Agent': 'Node.js App',
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({
        title: title,
        body: body
      })
    });

    if (!response.ok) {
      throw new Error(`GitHub API error: ${response.status} ${response.statusText}`);
    }

    const data = await response.json();
    console.log('Issue created:', data.html_url);
  } catch (error) {
    console.error('Error creating issue:', error.message);
  }
}















app.listen(3200)