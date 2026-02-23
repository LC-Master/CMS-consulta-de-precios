
export type Supplier = {
    id: number;
    SupplierName: string;
    AccountNumber: string;
    ContactName: string;
    EmailAddress: string;
    PhoneNumber: string;
    Notes: string;
}
export type SupplierOption = { value: string | number; label: string; original: Supplier };
