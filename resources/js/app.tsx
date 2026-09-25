import '../css/app.scss';

import { StrictMode } from 'react';
import { createRoot } from 'react-dom/client';
import { BrowserRouter, Route, Routes } from 'react-router-dom';
import { BasketProvider } from './contexts/Basket';
import Layout from './components/Layout';
import BasketPage from './pages/Basket';
import NotFound from './pages/NotFound';
import Products from './pages/Products';

createRoot(document.getElementById('root')!).render(
    <StrictMode>
        <BrowserRouter>
            <BasketProvider>
                <Routes>
                    <Route element={<Layout />}>
                        <Route index element={<Products />} />
                        <Route path="basket" element={<BasketPage />} />
                        <Route path="*" element={<NotFound />} />
                    </Route>
                </Routes>
            </BasketProvider>
        </BrowserRouter>
    </StrictMode>,
);
