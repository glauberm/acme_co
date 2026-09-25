import { useEffect, useState } from 'react';
import { formatMoney, getProducts, Product } from '../api';
import { useBasket } from '../contexts/Basket';
import ProductImage from '../components/ProductImage';

export default function Products() {
    const { add, busy } = useBasket();
    const [products, setProducts] = useState<Product[] | null>(null);
    const [error, setError] = useState<string | null>(null);

    useEffect(() => {
        getProducts()
            .then(setProducts)
            .catch((e: Error) => setError(e.message));
    }, []);

    if (error) {
        return <div className="alert alert-danger">{error}</div>;
    }

    if (!products) {
        return <div className="spinner-border text-primary" role="status" />;
    }

    return (
        <>
            <h1 className="h3 mb-4">Products</h1>

            <div className="row row-cols-1 row-cols-md-3 g-4">
                {products.map((product) => (
                    <div className="col" key={product.code}>
                        <div className="card h-100 shadow-sm">
                            <ProductImage code={product.code} className="card-img-top ratio ratio-1x1" />
                            <div className="card-body text-center">
                                <h2 className="h5 card-title">{product.name}</h2>
                                <p className="fs-4 mb-0">{formatMoney(product.price)}</p>
                            </div>
                            <div className="card-footer bg-transparent border-0 pb-3">
                                <button className="btn btn-outline-primary w-100" disabled={busy} onClick={() => add(product.code)}>
                                    Add to basket
                                </button>
                            </div>
                        </div>
                    </div>
                ))}
            </div>
        </>
    );
}
