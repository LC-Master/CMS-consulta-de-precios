import { RegionData, Store } from '@/types/store/index.type';
import { useMemo, useState, useEffect, CSSProperties } from 'react';
import Select, { MultiValue, ActionMeta, StylesConfig } from 'react-select';

interface SelectOption {
    value: string;
    label: string;
    isAll?: boolean;
    isRegionAll?: boolean;
    storeIds?: string[];
    region?: string;
}

interface GroupedOption {
    label: string;
    options: SelectOption[];
    quantity?: number;
}

const groupStyles = {
    display: 'flex',
    alignItems: 'center',
    justifyContent: 'space-between',
};

const groupBadgeStyles: CSSProperties = {
    backgroundColor: '#0052CC',
    borderRadius: '4px',
    color: '#fff',
    display: 'inline-block',
    fontSize: '10px',
    fontWeight: 'bold',
    padding: '2px 8px',
};

const customStyles: StylesConfig<SelectOption, true, GroupedOption> = {
    control: (base) => ({
        ...base,
        borderRadius: '0.375rem',
        borderColor: '#ccc',
        minHeight: '30px',
        boxShadow: 'none',
        '&:hover': { borderColor: '#0052CC' }
    }),
    option: (base, state) => ({
        ...base,
        fontSize: '13px',
        padding: '10px 15px',
        backgroundColor: state.isFocused ? '#f0f5ff' : 'white',
        color: state.isFocused ? '#0052CC' : '#333',
        cursor: 'pointer'
    }),
    multiValue: (base) => ({
        ...base,
        backgroundColor: '#e7effc',
        border: '1px solid #0052CC',
        borderRadius: '4px'
    }),
    multiValueLabel: (base) => ({
        ...base,
        color: '#0052CC',
        fontWeight: '600'
    })
};

const formatGroupLabel = (data: GroupedOption) => (
    <div style={groupStyles}>
        <span>{data.label}</span>
        <span style={groupBadgeStyles}>{data.quantity !== undefined ? data.quantity : data.options.length - 1} SUCURSALES</span>
    </div>
);

const buildStoreOption = (store: Store, region: string): SelectOption => ({
    value: store.id.toString(),
    label: `${store.name} (${store.store_code})`,
    region
});

const GroupedSelect = ({
    dataFromBackend,
    initialSelectedIds = [],
    onSelectionChange
}: {
    dataFromBackend: RegionData[];
    initialSelectedIds?: string[];
    onSelectionChange: (ids: string[]) => void;
}) => {
    const [selectedValues, setSelectedValues] = useState<SelectOption[]>([]);

    const options = useMemo<GroupedOption[]>(() => {
        if (!dataFromBackend || dataFromBackend.length === 0) {
            return [];
        }

        const totalStores = dataFromBackend.reduce((acc, curr) => acc + curr.stores.length, 0);

        const globalGroup: GroupedOption = {
            label: 'ADMINISTRACIÓN SISTEMA',
            quantity: totalStores,
            options: [
                {
                    value: 'GLOBAL_ALL',
                    label: 'SELECCIONAR TODAS LAS SUCURSALES (NACIONAL)',
                    isAll: true
                }
            ]
        };

        const regionGroups: GroupedOption[] = dataFromBackend.map((item) => ({
            label: item.region.toUpperCase(),
            quantity: item.stores.length,
            options: [
                {
                    value: `REGION_ALL_${item.region}`,
                    label: `SELECCIONAR TODA LA SOCIEDAD: ${item.region}`,
                    isRegionAll: true,
                    region: item.region,
                    storeIds: item.stores.map((s) => s.id.toString())
                },
                ...item.stores.map((store) => buildStoreOption(store, item.region))
            ]
        }));

        return [globalGroup, ...regionGroups];
    }, [dataFromBackend]);

    useEffect(() => {
        if (!dataFromBackend || dataFromBackend.length === 0) return;

        const calculateSelection = () => {
            if (!initialSelectedIds || initialSelectedIds.length === 0) {
                return [] as SelectOption[];
            }

            const allStoreIds = dataFromBackend.flatMap((region) => region.stores.map((store) => store.id.toString()));
            const isGlobalAll = allStoreIds.length > 0 && allStoreIds.every((id) => initialSelectedIds.includes(id));

            if (isGlobalAll) {
                const globalOption = options.find((group) => group.options.some((opt) => opt.isAll))
                    ?.options.find((opt) => opt.isAll);

                if (globalOption) {
                    return [globalOption];
                }
            }

            const nextSelection: SelectOption[] = [];

            dataFromBackend.forEach((region) => {
                const regionGroup = options.find((group) => group.label === region.region.toUpperCase());
                if (!regionGroup) return;

                const regionIds = region.stores.map((store) => store.id.toString());
                const isRegionAll = regionIds.length > 0 && regionIds.every((id) => initialSelectedIds.includes(id));

                if (isRegionAll) {
                    const regionAllOption = regionGroup.options.find((opt) => opt.isRegionAll);
                    if (regionAllOption) {
                        nextSelection.push(regionAllOption);
                    }
                } else {
                    region.stores.forEach((store) => {
                        const storeValue = store.id.toString();
                        if (initialSelectedIds.includes(storeValue)) {
                            const storeOption = regionGroup.options.find((opt) => opt.value === storeValue);
                            if (storeOption) {
                                nextSelection.push(storeOption);
                            }
                        }
                    });
                }
            });

            return nextSelection;
        };

        const timeoutId = setTimeout(() => {
            setSelectedValues(calculateSelection());
        }, 0);

        return () => clearTimeout(timeoutId);
    }, [dataFromBackend, initialSelectedIds, options]);

    const handleChange = (newValue: MultiValue<SelectOption>, actionMeta: ActionMeta<SelectOption>) => {
        let selectionArray = [...newValue] as SelectOption[];
        const action = actionMeta.action;
        const option = actionMeta.option;

        if (action === 'select-option' && option) {
            if (option.isAll) {
                selectionArray = [option];
            } else if (option.isRegionAll) {
                selectionArray = selectionArray.filter(
                    (opt) =>
                        opt.value === option.value ||
                        (!opt.isAll && opt.region !== option.region)
                );
            } else {
                const hasGlobal = selectionArray.some((opt) => opt.isAll);
                const regionAllOption = selectionArray.find(
                    (opt) => opt.isRegionAll && opt.region === option.region
                );

                if (hasGlobal) {
                    const allStores = dataFromBackend.flatMap((region) =>
                        region.stores.map((store) => buildStoreOption(store, region.region))
                    );
                    selectionArray = allStores.filter((storeOpt) => storeOpt.value !== option.value);
                } else if (regionAllOption) {
                    const regionStores = dataFromBackend.find((region) => region.region === option.region)
                        ?.stores || [];
                    const expandedStores = regionStores
                        .filter((store) => store.id.toString() !== option.value)
                        .map((store) => buildStoreOption(store, option.region || ''));

                    selectionArray = selectionArray.filter(
                        (opt) => opt.value !== regionAllOption.value && opt.value !== option.value
                    );
                    selectionArray = [...selectionArray, ...expandedStores];
                }
            }
        }

        setSelectedValues(selectionArray);

        let finalIds: string[] = [];
        selectionArray.forEach((opt) => {
            if (opt.isAll) {
                finalIds = dataFromBackend.flatMap((region) => region.stores.map((store) => store.id.toString()));
            } else if (opt.isRegionAll && opt.storeIds) {
                finalIds = [...finalIds, ...opt.storeIds];
            } else {
                finalIds.push(opt.value);
            }
        });

        onSelectionChange([...new Set(finalIds)]);
    };

    return (
        <div className="select-container" style={{ width: '100%', maxWidth: '600px' }}>
            <Select<SelectOption, true, GroupedOption>
                isMulti
                value={selectedValues}
                options={options}
                formatGroupLabel={formatGroupLabel}
                onChange={handleChange}
                placeholder="Búsqueda de sucursales..."
                isClearable
                closeMenuOnSelect={false}
                styles={customStyles}
                noOptionsMessage={() => 'No se encontraron resultados'}
            />
        </div>
    );
};

export default GroupedSelect;
