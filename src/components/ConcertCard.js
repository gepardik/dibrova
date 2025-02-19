import React from 'react';
import { useNavigate } from 'react-router-dom';

const ConcertCard = ({ concert }) => {
  const navigate = useNavigate();

  const handleClick = () => {
    navigate(`/concerts/${concert.id}`);
  };

  // Форматируем время, убирая секунды
  const formatTime = (timeString) => {
    return timeString.replace(/:\d{2}$/, '');
  };

  return (
    <div 
      className="concert-card"
      onClick={handleClick}
      role="button"
      tabIndex={0}
      onKeyPress={(e) => {
        if (e.key === 'Enter') {
          handleClick();
        }
      }}
    >
      <img 
        src={`/${concert.image_path}`}
        alt={concert.title} 
        className="concert-card__image"
      />
      <div className="concert-card__content">
        <h3 className="concert-card__title">{concert.title}</h3>
        <p className="concert-card__datetime">
          {concert.date}, {formatTime(concert.time)}
        </p>
        <p className="concert-card__venus">
          {concert.venue}
        </p>
      </div>
    </div>
  );
};

export default ConcertCard; 