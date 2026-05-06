import { useEffect, useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { useAuth } from '../../context/AuthContext';
import client from '../../api/client';

interface DashboardStats {
    resources: number;
    playlists: number;
    categories: number;
}

function DashboardPage() {
    const { isAuthenticated } = useAuth();
    const navigate = useNavigate();
    const [stats, setStats] = useState<DashboardStats | null>(null);
    const [resources, setResources] = useState<any[]>([]);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        if (!isAuthenticated) {
            navigate('/login');
            return;
        }
        Promise.all([
            client.get('/resources'),
            client.get('/playlists'),
            client.get('/categories'),
        ]).then(([res, pl, cat]) => {
            setStats({
                resources: res.data.data.length,
                playlists: pl.data.length,
                categories: cat.data.length,
            });
            setResources(res.data.data);
        }).finally(() => setLoading(false));
    }, [isAuthenticated, navigate]);

    const handleDelete = async (id: number) => {
        if (!confirm('Supprimer cette ressource ?')) return;
        try {
            await client.delete(`/resources/${id}`);
            setResources(prev => prev.filter(r => r.id !== id));
        } catch {
            alert('Erreur lors de la suppression');
        }
    };

    if (loading) return <p>Chargement...</p>;

    return (
        <div>
            <h1>Tableau de bord Admin</h1>

            {stats && (
                <div style={{ display: 'flex', gap: '20px', marginBottom: '30px' }}>
                    <div style={{ border: '1px solid #7c6dfa', padding: '20px', borderRadius: '8px' }}>
                        <h3>Ressources</h3>
                        <p style={{ fontSize: '32px', fontWeight: 'bold' }}>{stats.resources}</p>
                    </div>
                    <div style={{ border: '1px solid #7c6dfa', padding: '20px', borderRadius: '8px' }}>
                        <h3>Playlists</h3>
                        <p style={{ fontSize: '32px', fontWeight: 'bold' }}>{stats.playlists}</p>
                    </div>
                    <div style={{ border: '1px solid #7c6dfa', padding: '20px', borderRadius: '8px' }}>
                        <h3>Categories</h3>
                        <p style={{ fontSize: '32px', fontWeight: 'bold' }}>{stats.categories}</p>
                    </div>
                </div>
            )}

            <h2>Gestion des ressources</h2>
            <button onClick={() => navigate('/admin/resources/new')} style={{ marginBottom: '10px' }}>
                + Ajouter une ressource
            </button>
            <table style={{ width: '100%', borderCollapse: 'collapse' }}>
                <thead>
                <tr style={{ background: '#1a1a2e', color: 'white' }}>
                    <th style={{ padding: '10px', textAlign: 'left' }}>Titre</th>
                    <th style={{ padding: '10px', textAlign: 'left' }}>Type</th>
                    <th style={{ padding: '10px', textAlign: 'left' }}>Statut</th>
                    <th style={{ padding: '10px', textAlign: 'left' }}>Actions</th>
                </tr>
                </thead>
                <tbody>
                {resources.map(r => (
                    <tr key={r.id} style={{ borderBottom: '1px solid #ccc' }}>
                        <td style={{ padding: '10px' }}>{r.title}</td>
                        <td style={{ padding: '10px' }}>{r.type}</td>
                        <td style={{ padding: '10px' }}>{r.isAvailable ? 'Disponible' : 'Emprunté'}</td>
                        <td style={{ padding: '10px', display: 'flex', gap: '10px' }}>
                            <button onClick={() => navigate(`/admin/resources/${r.id}/edit`)}>Modifier</button>
                            <button onClick={() => handleDelete(r.id)} style={{ color: 'red' }}>Supprimer</button>
                        </td>
                    </tr>
                ))}
                </tbody>
            </table>
        </div>
    );
}

export default DashboardPage;
