import { useState } from 'react'
import reactLogo from './assets/react.svg'
import viteLogo from '/vite.svg'
import axios from 'axios'
import './App.css'

function App() {
  const [messages, setMessages] = useState([]);
  const [input, setInput] = useState("");
  const [username, setUsername] = useState("student1");
  // setUsername="Prakash Parajuli";
  const sendMessage = async (e) => {
    e.preventDefault();
    if(!input.trim()) return;

    const userMessage = {text:input, sender:"user"};
    setMessages([...messages, userMessage]);
    setInput("");
    try {
      const response = await axios.post("http://localhost:5000/api/chat", {
        message: input,
        username,
      });
      const botMessage = { text: response.data.reply, sender: "bot" };
      setMessages((prev) => [...prev, botMessage]);
    } catch (error) {
      const errorMessage = { text: 'Error communicating with the server.'+error, sender:'bot' };
      setMessages((prev) => [...prev, errorMessage]);
    }
  };

  return (
    <div className='min-h-screen bg-gradient-to-br from-blue-100 to-purple-100 flex items-center justify-center p-4'>
      <div className="w-full max-w-2xl bg-white rounded-2xl shadow-xl flex flex-col h-[80vh]">
        <div className='bg-blue-600 text-white p-4 rounded-t-2xl'>
          <h1 className='text-xl font-bold'>St. Lawrence College Chatbot</h1>
          <p className='text-sm'>Ask about your schedule or upcoming events!</p>
        </div>
        <div className='flex-1 p-4 overflow-y-auto'>
          {messages.map((msg,index)=>(
            <div key={index} className={`mb-4 flex ${msg.sender==="user"? 'justify-start':'justify-end'}`}>
              <div className={`max-w-[70%] p-3 rounded-lg ${msg.sender==='user' ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-800 text-left'}`}>
                <p className='whitespace-pre-wrap' dangerouslySetInnerHTML={{__html: msg.text}}></p>
              </div>
            </div>
          ))}
        </div>
        <form onSubmit={sendMessage} className='p-4 border-t'>
          <div className='flex gap-2'>
            <input type='text' value={input} onChange={(e)=>setInput(e.target.value)} placeholder='Type your message...' className='flex-1 p-2 border rounded-1g focus:outline-none focus:ring-2 focus:ring-blue-500' />
            <button type='submit' className='bg-blue-600 text-white px-4 py-2 rounded-1g hover:bg-blue-700 transition'>Send</button>
          </div>
        </form>
      </div>
    </div>
  );
}

export default App
