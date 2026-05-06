import { useEffect, useState } from 'react';
import { useParams } from 'react-router-dom';
import { useAuth } from '../context/AuthContext';
import client from '../api/client';

interface Resource {
    id: number;
    title: string;
    type: string;
    description: string;
    url: string | null;
    publishedAt: string;
    isAvailable: boolean;
    playlist: { id: number; name: string } | null;
}

interface Review {
    id: number;
    rating: number;
    comment: string;
    createdAt: string;
    user: { firstName: string; lastName: string };
}

function ResourceDetailPage() {
    const { id } = useParams();
    const { isAuthenticated } = useAuth();
    const [resource, setResource] = useState<Resource | null>(null);
    const [reviews, setReviews] = useState<Review[]>([]);
    const [average, setAverage] = useState<number | null>(null);
    const [loading, setLoading] = useState(true);
    const [message, setMessage] = useState('');
    const [rating, setRating] = useState(5);
    const [comment, setComment] = useState('');

    useEffect(() => {
        Promise.all([
            client.get(`/resources/${id}`),
            client.get(`/reviews/resource/${id}`),
        ]).then(([resResource, resReviews]) => {
            setResource(resResource.data);
            setReviews(resReviews.data.reviews);
            setAverage(resReviews.data.average);
        }).finally(() => setLoading(false));
    }, [id]);

    const handleBorrow = async () => {
        try {
            await client.post('/borrows', { resourceId: Number(id) });
            setMessage('Emprunt enregistré avec succès !');
            setResource(prev => prev ? { ...prev, isAvailable: false } : prev);
        } catch {
            setMessage('Erreur lors de l\'emprunt');
        }
    };

    const handleReview = async (e: React.FormEvent) => {
        e.preventDefault();
        try {
            await client.post('/reviews', { resourceId: Number(id), rating, comment });
            setMessage('Avis soumis, en attente de modération');
            setComment('');
        } catch {
            setMessage('Erreur lors de la soumission de l\'avis');
        }
    };

    if (loading) return <p>Chargement...</p>;
    if (!resource) return <p>Ressource introuvable</p>;

    return (
        <div>
            <h1>{resource.title}</h1>
            <p>Type : {resource.type}</p>
            <p>Playlist : {resource.playlist?.name ?? 'Aucune'}</p>
            <p>Publié le : {resource.publishedAt}</p>
            <p>Statut : {resource.isAvailable ? 'Disponible' : 'Emprunté'}</p>
            {resource.description && <p>{resource.description}</p>}
            {resource.url && <a href={resource.url} target="_blank" rel="noreferrer">Accéder à la ressource</a>}

            {message && <p style={{ color: 'green' }}>{message}</p>}

            {isAuthenticated && resource.isAvailable && (
                <button onClick={handleBorrow}>Emprunter</button>
            )}

            <h2>Avis {average ? `— Note moyenne : ${average}/5` : ''}</h2>
            {reviews.length === 0 && <p>Aucun avis pour le moment</p>}
            {reviews.map(r => (
                <div key={r.id} style={{ border: '1px solid #ccc', margin: '10px', padding: '10px' }}>
                    <p><strong>{r.user.firstName} {r.user.lastName}</strong> — {r.rating}/5</p>
                    <p>{r.comment}</p>
                    <p><small>{r.createdAt}</small></p>
                </div>
            ))}

            {isAuthenticated && (
                <div>
                    <h3>Laisser un avis</h3>
                    <form onSubmit={handleReview}>
                        <div>
                            <label>Note (1-5)</label>
                            <input type="number" min={1} max={5} value={rating} onChange={e => setRating(Number(e.target.value))} />
                        </div>
                        <div>
                            <label>Commentaire</label>
                            <textarea value={comment} onChange={e => setComment(e.target.value)} />
                        </div>
                        <button type="submit">Soumettre</button>
                    </form>
                </div>
            )}
        </div>
    );
}

export default ResourceDetailPage;
