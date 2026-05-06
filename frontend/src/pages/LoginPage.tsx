import { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { useAuth } from '../context/AuthContext';
import axios from 'axios';

function LoginPage() {
    const [email, setEmail] = useState('');
    const [password, setPassword] = useState('');
    const [error, setError] = useState('');
    const { login } = useAuth();
    const navigate = useNavigate();

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        setError('');
        try {
            const res = await axios.post('/api/login_check', { email, password });
            login(res.data.token);
            navigate('/');
        } catch {
            setError('Email ou mot de passe incorrect');
        }
    };

    return (
        <div>
            <h1>Connexion</h1>
            {error && <p style={{ color: 'red' }}>{error}</p>}
            <form onSubmit={handleSubmit}>
                <div>
                    <label>Email</label>
                    <input type="email" value={email} onChange={e => setEmail(e.target.value)} required />
                </div>
                <div>
                    <label>Mot de passe</label>
                    <input type="password" value={password} onChange={e => setPassword(e.target.value)} required />
                </div>
                <button type="submit">Se connecter</button>
            </form>
            <p>Pas de compte ? <a href="/register">S'inscrire</a></p>
        </div>
    );
}

export default LoginPage;
