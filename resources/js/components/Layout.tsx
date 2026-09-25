import { Link, NavLink, Outlet } from 'react-router-dom';
import { formatMoney } from '../api';
import { useBasket } from '../contexts/Basket';

export default function Layout() {
    const { basket, error, dismissError, toast, dismissToast } = useBasket();
    const count = basket?.items.reduce((sum, item) => sum + item.quantity, 0) ?? 0;

    return (
        <>
            <nav className="navbar navbar-expand bg-body-tertiary border-bottom mb-4">
                <div className="container">
                    <Link className="navbar-brand fw-semibold" to="/">
                        AcmeCo
                    </Link>
                    <div className="navbar-nav me-auto">
                        <NavLink className="nav-link" to="/" end>
                            Products
                        </NavLink>
                    </div>
                    <NavLink className="btn btn-primary" to="/basket">
                        Basket <span className="badge text-bg-light ms-1">{count}</span>
                        {basket && count > 0 && <span className="ms-2">{formatMoney(basket.total)}</span>}
                    </NavLink>
                </div>
            </nav>

            <main className="container pb-5">
                {error && (
                    <div className="alert alert-danger alert-dismissible">
                        {error}
                        <button type="button" className="btn-close" aria-label="Close" onClick={dismissError} />
                    </div>
                )}
                <Outlet />
            </main>

            {toast && (
                <div className="toast-container position-fixed bottom-0 end-0 p-3">
                    <div className="toast show text-bg-success border-0" role="status">
                        <div className="d-flex">
                            <div className="toast-body">{toast}</div>
                            <button type="button" className="btn-close btn-close-white me-2 m-auto" aria-label="Close" onClick={dismissToast} />
                        </div>
                    </div>
                </div>
            )}

            <footer className="container border-top py-4 text-center text-body-secondary small">
                Made with ♥ by{' '}
                <a className="link-secondary" href="https://github.com/glauberm/acme_co">
                    Glauber Mota
                </a>
            </footer>
        </>
    );
}
