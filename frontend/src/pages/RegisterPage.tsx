import { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import client from '../api/client';

function RegisterPage() {
    const [form, setForm] = useState({
        email: '',
        password: '',
        firstName: '',
        lastName: '',
    });
    const [error, setError] = useState('');
    const [success, setSuccess] = useState('');
    const navigate = useNavigate();

    const handleChange = (e: React.ChangeEvent<HTMLInputElement>) => {
        setForm({ ...form, [e.target.name]: e.target.value });
    };

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        setError('');
        try {
            await client.post('/auth/register', form);
            setSuccess('Compte créé avec succès !');
            setTimeout(() => navigate('/login'), 2000);
        } catch {
            setError('Erreur lors de la création du compte');
        }
    };

    return (
        <div>
            <h1>Inscription</h1>
            {error && <p style={{ color: 'red' }}>{error}</p>}
            {success && <p style={{ color: 'green' }}>{success}</p>}
            <form onSubmit={handleSubmit}>
                <div>
                    <label>Prénom</label>
                    <input name="firstName" value={form.firstName} onChange={handleChange} required />
                </div>
                <div>
                    <label>Nom</label>
                    <input name="lastName" value={form.lastName} onChange={handleChange} required />
                </div>
                <div>
                    <label>Email</label>
                    <input type="email" name="email" value={form.email} onChange={handleChange} required />
                </div>
                <div>
                    <label>Mot de passe</label>
                    <input type="password" name="password" value={form.password} onChange={handleChange} required />
                </div>
                <button type="submit">S'inscrire</button>
            </form>
        </div>
    );
}

export default RegisterPage;
