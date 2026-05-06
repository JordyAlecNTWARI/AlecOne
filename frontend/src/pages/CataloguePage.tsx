import { useEffect, useState } from 'react';
import client from '../api/client';

interface Resource {
    id: number;
    title: string;
    type: string;
    description: string;
    isAvailable: boolean;
    playlist: { id: number; name: string } | null;
}

function CataloguePage() {
    const [resources, setResources] = useState<Resource[]>([]);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState('');
    const [type, setType] = useState('');

    useEffect(() => {
        const params = type ? `?type=${type}` : '';
        client.get(`/resources${params}`)
            .then(res => setResources(res.data.data))
            .catch(() => setError('Erreur lors du chargement'))
            .finally(() => setLoading(false));
    }, [type]);

    if (loading) return <p>Chargement...</p>;
    if (error) return <p style={{ color: 'red' }}>{error}</p>;

    return (
        <div>
            <h1>Catalogue</h1>
            <select value={type} onChange={e => setType(e.target.value)}>
                <option value="">Tous les types</option>
                <option value="Formation">Formation</option>
                <option value="Livre">Livre</option>
                <option value="Video">Video</option>
            </select>
            <div>
                {resources.length === 0 && <p>Aucune ressource disponible</p>}
                {resources.map(r => (
                    <div key={r.id} style={{ border: '1px solid #ccc', margin: '10px', padding: '10px' }}>
                        <h2>{r.title}</h2>
                        <p>Type : {r.type}</p>
                        <p>Playlist : {r.playlist?.name ?? 'Aucune'}</p>
                        <p>Statut : {r.isAvailable ? 'Disponible' : 'Emprunté'}</p>
                        <p>{r.description}</p>
                    </div>
                ))}
            </div>
        </div>
    );
}

export default CataloguePage;
