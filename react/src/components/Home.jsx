import React, { useEffect, useState } from 'react';
import Cookies from 'js-cookie';
import { getUserInfo } from '../api';
import { useNavigate } from 'react-router-dom';

export default function Home() {
    const [user, setUser] = useState(null);

    useEffect(() => {
        const fetchUser = async () => {
            const token = Cookies.get('token');
            if (!token) {
                window.location.href = '/login';
                return;
            }

            const result = await getUserInfo(token);
            if (result.username) {
                setUser(result);
            } else {
                Cookies.remove('token');
                window.location.href = '/login';
            }
        };

        fetchUser();
    }, []);
    const navigate = useNavigate()
    const handleLogout = () => {
        Cookies.remove('token');
        navigate('/login')
      };

    if (!user) {
        return <div className="container">Loading...</div>;
    }

    return (
        <div className="container">
            <h1>Welcome, {user.username}!</h1>
            <p>Email: {user.email}</p>
            <button onClick={handleLogout}>Logout</button>
        </div>
    );
}
