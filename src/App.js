import React, { Suspense } from 'react';
import { Routes, Route } from 'react-router-dom';
import Navbar from './components/Navbar';
import Footer from './components/Footer';

// Lazy load page components
const Home = React.lazy(() => import('./pages/Home'));
const Concerts = React.lazy(() => import('./pages/Concerts'));
const ConcertDetails = React.lazy(() => import('./pages/ConcertDetails'));
const Contact = React.lazy(() => import('./pages/Contact'));

// Loading component
const Loading = () => (
  <div style={{ 
    padding: '20px', 
    textAlign: 'center',
    color: '#1C2824'
  }}>
    Loading...
  </div>
);

const App = () => {
  return (
    <div className="app">
      <Navbar />
      <main>
        <Suspense fallback={<Loading />}>
          <Routes>
            <Route path="/" element={<Home />} />
            <Route path="/concerts" element={<Concerts />} />
            <Route path="/concerts/:id" element={<ConcertDetails />} />
            <Route path="/contact" element={<Contact />} />
          </Routes>
        </Suspense>
      </main>
      <Footer />
    </div>
  );
};

export default App; 