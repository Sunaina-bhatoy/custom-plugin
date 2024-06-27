import React, { useState } from 'react';
import Cookies from 'js-cookie';
import { Link } from 'react-router-dom';
import { login } from '../api';

export default function Login() {
    const [username, setUsername] = useState('');
    const [password, setPassword] = useState('');
    const [error, setError] = useState('');

    const handleLogin = async () => {
        const result = await login(username, password);
        if (result.token) {
            Cookies.set('token', result.token);
            window.location.href = '/';
        } else {
            setError(result.message || 'Login failed');
        }
    };

    return (
        <div className="container">
            <h1>Login</h1>
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
            <button onClick={handleLogin}>Login</button>
            {error && <p className="error">{error}</p>}
            <p>Don't have an account? <Link to="/signup">Signup here</Link></p>
        </div>
    );
}
