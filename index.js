import express from 'express'
import path from 'path'
import { fileURLToPath } from 'url';
import AOS from 'aos';



const app=express()
app.set("view engine","ejs")
const pathName=path.resolve('assets')

const folderName='img'
app.use(express.static(path.join(pathName,folderName)))


app.get("/",(req,resp)=>{
 const absPath=path.resolve('index.html')
resp.sendFile(absPath)
console.log(absPath)
const folderName='img'
app.use('/img',express.static(path.join(pathName,'img')))
console.log((path.join(pathName,folderName)));

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