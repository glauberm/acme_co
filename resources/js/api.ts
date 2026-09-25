export type Product = {
    code: string;
    name: string;
    price: number;
};

export type BasketItem = Product & {
    quantity: number;
};

export type Basket = {
    items: BasketItem[];
    subtotal: number;
    discount: number;
    delivery: number;
    total: number;
};

async function request<T>(method: string, path: string, body?: object): Promise<T> {
    const response = await fetch(`/api/${path}`, {
        method,
        headers: { Accept: 'application/json', 'Content-Type': 'application/json' },
        body: body && JSON.stringify(body),
    });
    const json = await response.json();

    if (!response.ok) {
        throw new Error(json.message ?? `Request failed with status ${response.status}.`);
    }

    return json.data;
}

export const getProducts = () => request<Product[]>('GET', 'products');
export const getBasket = () => request<Basket>('GET', 'basket');
export const addItem = (code: string) => request<Basket>('POST', 'basket/items', { code });
export const removeItem = (code: string) => request<Basket>('DELETE', `basket/items/${code}`);
export const clearBasket = () => request<Basket>('DELETE', 'basket');

export const formatMoney = (cents: number) => `$${(cents / 100).toFixed(2)}`;
