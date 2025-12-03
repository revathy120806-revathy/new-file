import express from 'express'
import path from 'path'
import { fileURLToPath } from 'url';




const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

const app=express()
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



app.listen(3200)