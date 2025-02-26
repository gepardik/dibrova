import React, { useState, useEffect } from 'react';
import { useTranslation } from 'react-i18next';
import ConcertCard from '../components/ConcertCard';

const Concerts = () => {
  const { t, i18n } = useTranslation();
  const [upcomingConcerts, setUpcomingConcerts] = useState([]);
  const [pastConcerts, setPastConcerts] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);
  const [activeTab, setActiveTab] = useState('upcoming');

  useEffect(() => {
    const fetchConcerts = async () => {
      try {
        setLoading(true);
        const response = await fetch(`/api/concerts.php?lang=${i18n.language}`);
        if (!response.ok) {
          throw new Error('Failed to fetch concerts');
        }
        const data = await response.json();
        setUpcomingConcerts(data.upcoming);
        setPastConcerts(data.past);
        setActiveTab(data.upcoming.length > 0 ? 'upcoming' : 'past');
        setError(null);
      } catch (err) {
        setError(err.message);
      } finally {
        setLoading(false);
      }
    };

    fetchConcerts();
  }, [i18n.language]);

  if (loading) {
    return <div className="concerts__loading">{t('common.loading')}...</div>;
  }

  if (error) {
    return <div className="concerts__error">Error: {error}</div>;
  }

  return (
    <div className="concerts">
      <div className="concerts__tabs">
        <button 
          className={`concerts__tab ${activeTab === 'upcoming' ? 'active' : ''}`}
          onClick={() => setActiveTab('upcoming')}
        >
          {t('concerts.upcoming')}
        </button>
        <button 
          className={`concerts__tab ${activeTab === 'past' ? 'active' : ''}`}
          onClick={() => setActiveTab('past')}
        >
          {t('concerts.past')}
        </button>
      </div>

      <div className="concerts__content">
        {activeTab === 'upcoming' ? (
          <div className="concerts__grid">
            {upcomingConcerts.length > 0 ? (
              upcomingConcerts.map((concert) => (
                <ConcertCard key={concert.id} concert={concert} />
              ))
            ) : (
              <p className="concerts__empty">{t('concerts.noUpcoming')}</p>
            )}
          </div>
        ) : (
          <div className="concerts__grid">
            {pastConcerts.length > 0 ? (
              pastConcerts.map((concert) => (
                <ConcertCard key={concert.id} concert={concert} />
              ))
            ) : (
              <p className="concerts__empty">{t('concerts.noPast')}</p>
            )}
          </div>
        )}
      </div>
    </div>
  );
};

export default Concerts; 