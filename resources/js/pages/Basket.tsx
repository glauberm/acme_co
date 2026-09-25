import { Link } from 'react-router-dom';
import { formatMoney } from '../api';
import { useBasket } from '../contexts/Basket';
import ProductImage from '../components/ProductImage';

export default function BasketPage() {
    const { basket, busy, add, remove, clear } = useBasket();

    if (!basket) {
        return <div className="spinner-border text-primary" role="status" />;
    }

    if (basket.items.length === 0) {
        return (
            <div className="text-center py-5">
                <p className="lead">Your basket is empty.</p>
                <Link className="btn btn-primary" to="/">
                    Browse products
                </Link>
            </div>
        );
    }

    return (
        <>
            <div className="d-flex justify-content-between align-items-center mb-4">
                <h1 className="h3 mb-0">Basket</h1>
                <button className="btn btn-outline-danger btn-sm" disabled={busy} onClick={clear}>
                    Empty basket
                </button>
            </div>

            <table className="table align-middle">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th className="text-end">Total</th>
                    </tr>
                </thead>
                <tbody>
                    {basket.items.map((item) => (
                        <tr key={item.code}>
                            <td>
                                <div className="d-flex align-items-center gap-3">
                                    <ProductImage code={item.code} className="rounded flex-shrink-0" size={40} />
                                    {item.name}
                                </div>
                            </td>
                            <td>{formatMoney(item.price)}</td>
                            <td>
                                <div className="btn-group btn-group-sm">
                                    <button className="btn btn-outline-secondary" disabled={busy} onClick={() => remove(item.code)} aria-label="Remove one">
                                        −
                                    </button>
                                    <span className="btn btn-outline-secondary disabled text-body">{item.quantity}</span>
                                    <button className="btn btn-outline-secondary" disabled={busy} onClick={() => add(item.code)} aria-label="Add one">
                                        +
                                    </button>
                                </div>
                            </td>
                            <td className="text-end">{formatMoney(item.price * item.quantity)}</td>
                        </tr>
                    ))}
                </tbody>
            </table>

            <div className="row">
                <div className="col-md-5 ms-auto">
                    <ul className="list-group">
                        <li className="list-group-item d-flex justify-content-between">
                            Subtotal <span>{formatMoney(basket.subtotal)}</span>
                        </li>
                        {basket.discount > 0 && (
                            <li className="list-group-item d-flex justify-content-between text-success">
                                Offer discount <span>−{formatMoney(basket.discount)}</span>
                            </li>
                        )}
                        <li className="list-group-item d-flex justify-content-between">
                            Delivery <span>{basket.delivery > 0 ? formatMoney(basket.delivery) : 'Free'}</span>
                        </li>
                        <li className="list-group-item d-flex justify-content-between fw-bold fs-5">
                            Total <span>{formatMoney(basket.total)}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </>
    );
}
