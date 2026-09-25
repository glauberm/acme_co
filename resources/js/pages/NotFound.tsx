import { Link } from 'react-router-dom';

export default function NotFound() {
    return (
        <div className="text-center py-5">
            <h1 className="display-5">404</h1>
            <p className="lead">This page doesn't exist.</p>
            <Link className="btn btn-primary" to="/">
                Browse products
            </Link>
        </div>
    );
}
