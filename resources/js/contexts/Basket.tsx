import { createContext, ReactNode, useContext, useEffect, useRef, useState } from 'react';
import { addItem, Basket, clearBasket, getBasket, removeItem } from '../api';

type BasketContextValue = {
    basket: Basket | null;
    busy: boolean;
    error: string | null;
    dismissError: () => void;
    toast: string | null;
    dismissToast: () => void;
    add: (code: string) => Promise<Basket | null>;
    remove: (code: string) => Promise<Basket | null>;
    clear: () => Promise<Basket | null>;
};

const BasketContext = createContext<BasketContextValue | null>(null);

export function BasketProvider({ children }: { children: ReactNode }) {
    const [basket, setBasket] = useState<Basket | null>(null);
    const [busy, setBusy] = useState(false);
    const [error, setError] = useState<string | null>(null);
    const [toast, setToast] = useState<string | null>(null);
    const toastTimeout = useRef<number>(undefined);

    async function perform(request: () => Promise<Basket>) {
        setBusy(true);
        setError(null);

        try {
            const updated = await request();
            setBasket(updated);
            return updated;
        } catch (e) {
            setError((e as Error).message);
            return null;
        } finally {
            setBusy(false);
        }
    }

    async function add(code: string) {
        const updated = await perform(() => addItem(code));
        const item = updated?.items.find((item) => item.code === code);

        if (item) {
            setToast(`${item.name} added to basket.`);
            clearTimeout(toastTimeout.current);
            toastTimeout.current = window.setTimeout(() => setToast(null), 3000);
        }

        return updated;
    }

    useEffect(() => {
        perform(getBasket);
    }, []);

    return (
        <BasketContext.Provider
            value={{
                basket,
                busy,
                error,
                dismissError: () => setError(null),
                toast,
                dismissToast: () => setToast(null),
                add,
                remove: (code) => perform(() => removeItem(code)),
                clear: () => perform(clearBasket),
            }}
        >
            {children}
        </BasketContext.Provider>
    );
}

export function useBasket() {
    return useContext(BasketContext)!;
}
