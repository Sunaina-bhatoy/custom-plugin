import React, { useState } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import { signup } from '../api';

export default function Signup() {
    const navigate = useNavigate()
    const [email, setEmail] = useState('');
    const [username, setUsername] = useState('');
    const [password, setPassword] = useState('');
    const [message, setMessage] = useState('');

    const handleSignup = async () => {
        const result = await signup(email, username, password);
        setMessage(result.message || 'Signup failed');
        if(result.message === "User registered successfully") {
            navigate('/login');
        }
    };

    return (
        <div className="container">
            <h1>Signup</h1>
            <input
                type="email"
                placeholder="Email"
                value={email}
                onChange={(e) => setEmail(e.target.value)}
            />
            <input
                type="text"
                placeholder="Username"
                value={username}
                onChange={(e) => setUsername(e.target.value)}
            />
            <input
                type="password"
                placeholder="Password"
                value={password}
                onChange={(e) => setPassword(e.target.value)}
            />
            <button onClick={handleSignup}>Signup</button>
            {message && <p>{message}</p>}
            <p>Already have an account? <Link to="/login">Login here</Link></p>

        </div>
    );
}
