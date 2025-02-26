import React from 'react';
import { useTranslation } from 'react-i18next';

const Home = () => {
  const { t } = useTranslation();

  return (
    <div className="home">
      <div className="home__hero">
        <img src="/images/ensemble.jpg" alt="DIBROVA Ensemble" className="home__hero-image" />
      </div>
      <img src="/images/oak_leafe.svg" alt="" className="home__background-leaf" aria-hidden="true" />
      <div className="home__content">
        <h1 className="home__title" translate='no'>{t('home.title')}</h1>
        <div 
          translate="no"
          className="home__description"
          dangerouslySetInnerHTML={{ __html: t('home.description') }}
        />
      </div>
    </div>
  );
};

export default Home; 