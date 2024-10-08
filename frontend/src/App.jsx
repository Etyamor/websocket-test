// File: src/App.jsx
import React, { useState, useEffect } from 'react';
import './index.css';

const App = () => {
    const [socket, setSocket] = useState(null);
    const [messages, setMessages] = useState([]);
    const [message, setMessage] = useState('');

    useEffect(() => {
        const newSocket = new WebSocket('ws://localhost:8080/chat?username=JohnDoe');
        setSocket(newSocket);

        newSocket.onopen = () => {
            console.log('Connected to WebSocket server at ws://localhost:8080');
        };

        newSocket.onmessage = (event) => {
            setMessages((prevMessages) => [...prevMessages, event.data]);
        };

        newSocket.onclose = () => {
            console.log('Disconnected from WebSocket server');
        };

        newSocket.onerror = (error) => {
            console.error('WebSocket error:', error);
        };

        return () => {
            newSocket.close();
        };
    }, []);

    const sendMessage = () => {
        if (socket && message.trim()) {
            socket.send(message);
            setMessage('');
        }
    };

    return (
        <div className="App">
            <h2>WebSocket Chat Test</h2>
            <div>
                <h3>Messages:</h3>
                {messages.map((msg, index) => (
                    <p key={index}>{msg}</p>
                ))}
            </div>
            <input
                type="text"
                value={message}
                onChange={(e) => setMessage(e.target.value)}
                placeholder="Type your message..."
            />
            <button onClick={sendMessage}>Send</button>
        </div>
    );
};

export default App;