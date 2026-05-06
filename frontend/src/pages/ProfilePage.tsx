import { useEffect, useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { useAuth } from '../context/AuthContext';
import client from '../api/client';

interface Borrow {
    id: number;
    resource: { id: number; title: string; type: string };
    borrowedAt: string;
    dueAt: string;
    returnedAt: string | null;
    status: string;
}

function ProfilePage() {
    const { isAuthenticated, logout } = useAuth();
    const navigate = useNavigate();
    const [borrows, setBorrows] = useState<Borrow[]>([]);
    const [loading, setLoading] = useState(true);
    const [message, setMessage] = useState('');

    useEffect(() => {
        if (!isAuthenticated) {
            navigate('/login');
            return;
        }
        client.get('/borrows/mine')
            .then(res => setBorrows(res.data))
            .finally(() => setLoading(false));
    }, [isAuthenticated, navigate]);

    const handleReturn = async (id: number) => {
        try {
            await client.put(`/borrows/${id}/return`, {});
            setMessage('Ressource retournée avec succès');
            setBorrows(prev => prev.map(b =>
                b.id === id ? { ...b, status: 'RETOURNE', returnedAt: new Date().toISOString() } : b
            ));
        } catch {
            setMessage('Erreur lors du retour');
        }
    };

    if (loading) return <p>Chargement...</p>;

    return (
        <div>
            <h1>Mon profil</h1>
            <button onClick={() => { logout(); navigate('/'); }}>Se déconnecter</button>

            {message && <p style={{ color: 'green' }}>{message}</p>}

            <h2>Mes emprunts</h2>
            {borrows.length === 0 && <p>Aucun emprunt en cours</p>}
            {borrows.map(b => (
                <div key={b.id} style={{ border: '1px solid #ccc', margin: '10px', padding: '10px' }}>
                    <h3>{b.resource.title}</h3>
                    <p>Type : {b.resource.type}</p>
                    <p>Emprunté le : {b.borrowedAt}</p>
                    <p>À rendre avant le : {b.dueAt}</p>
                    <p>Statut : {b.status}</p>
                    {b.status === 'EN_COURS' && (
                        <button onClick={() => handleReturn(b.id)}>Retourner</button>
                    )}
                </div>
            ))}
        </div>
    );
}

export default ProfilePage;
