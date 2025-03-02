import React, { useState, useEffect } from 'react';
import { useParams, useNavigate } from 'react-router-dom';
import { useTranslation } from 'react-i18next';

const ConcertDetails = () => {
  const { id } = useParams();
  const navigate = useNavigate();
  const { t, i18n } = useTranslation();
  
  const [concert, setConcert] = useState(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);

  useEffect(() => {
    const fetchConcertDetails = async () => {
      try {
        setLoading(true);
        const url = `/api/concerts.php?id=${id}&lang=${i18n.language}`;
        console.log('Fetching concert details from:', url);
        
        const response = await fetch(url);
        console.log('Response status:', response.status);
        
        const data = await response.json();
        console.log('Raw API response:', data);

        // Если API вернул список концертов, найдем нужный концерт
        let concertData = data;
        if (data.upcoming || data.past) {
          const allConcerts = [...(data.upcoming || []), ...(data.past || [])];
          concertData = allConcerts.find(c => c.id === parseInt(id));
          if (!concertData) {
            throw new Error(t('concerts.notFound'));
          }
        }
        
        if (!response.ok) {
          if (data.error) {
            console.error('API error:', data.error);
            if (data.debug_data) {
              console.log('Debug data:', data.debug_data);
            }
          }
          throw new Error(response.status === 404 
            ? t('concerts.notFound') 
            : data.error || t('concerts.fetchError'));
        }

        // Проверяем структуру данных
        console.log('Concert data:', concertData);
        
        if (!concertData || typeof concertData !== 'object') {
          console.error('Invalid concert data format:', concertData);
          throw new Error('Invalid response format');
        }

        // Проверяем наличие обязательных полей
        const requiredFields = ['title', 'date', 'time', 'venue'];
        const missingFields = requiredFields.filter(field => !concertData[field]);
        
        if (missingFields.length > 0) {
          console.error('Missing required fields:', missingFields);
          console.log('Available fields:', Object.keys(concertData));
          console.log('Field values:', {
            title: concertData.title,
            date: concertData.date,
            time: concertData.time,
            venue: concertData.venue
          });
          throw new Error(`Missing required fields: ${missingFields.join(', ')}`);
        }

        // Преобразуем поля для соответствия ожидаемому формату
        concertData = {
          ...concertData,
          image: concertData.image ? `/${concertData.image}` : null,
          ticketLink: concertData.ticketLink || null
        };

        console.log('Setting concert data:', concertData);
        setConcert(concertData);
      } catch (err) {
        console.error('Error details:', err);
        setError(err.message);
      } finally {
        setLoading(false);
      }
    };

    fetchConcertDetails();
  }, [id, i18n.language, t]);

  console.log('Current state:', { loading, error, concert });

  if (loading) {
    return (
      <div className="concert-details">
        <div className="concert-details__loading">
          {t('common.loading')}...
        </div>
      </div>
    );
  }

  if (error) {
    return (
      <div className="concert-details">
        <div className="concert-details__error">
          {error}
        </div>
      </div>
    );
  }

  if (!concert) {
    return (
      <div className="concert-details">
        <div className="concert-details__not-found">
          {t('concerts.notFound')}
        </div>
      </div>
    );
  }

  console.log('Rendering concert details:', concert);

  return (
    <div className="concert-details">
      <button 
        className="concert-details__back-button"
        onClick={() => navigate(-1)}
      >
        ← {t('common.back')}
      </button>

      <div className="concert-details__content">
        {concert.image && (
          <div className="concert-details__image-container">
            <img 
              src={concert.image} 
              alt={concert.title} 
              className="concert-details__image"
            />
          </div>
        )}

        <span className="concert-details__container">
          <h1 className="concert-details__title">{concert.title || 'Untitled Concert'}</h1>
          <div className="concert-details__metadata">
            <p className="concert-details__date">
              <strong>{t('concerts.date')}:</strong> {concert.date}
            </p>
            <p className="concert-details__time">
              <strong>{t('concerts.time')}:</strong> {concert.time}
            </p>
            <p className="concert-details__venue">
              <strong>{t('concerts.venue')}:</strong> {concert.venue}
            </p>
            {concert.price && (
              <p className="concert-details__price">
                <strong>{t('concerts.price')}:</strong> {concert.price !== '0' ? `${concert.price} €` : `${t('concerts.free')}`}
              </p>
            )}
          </div>
        </span>

        {concert.description && (
            <div 
              className="concert-details__description"
              dangerouslySetInnerHTML={{ __html: concert.description }}
            />
          )}



        {concert.ticketLink && (
          <a 
            href={concert.ticketLink}
            target="_blank"
            rel="noopener noreferrer"
            className="concert-details__ticket-button"
          >
            {t('concerts.buyTickets')}
          </a>
        )}
      </div>
    </div>
  );
};

export default ConcertDetails; 