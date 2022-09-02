const getters = {
    active: state => (state.toolModals.length > 0 ? state.toolModals[0] : null),
    allModals: state => state.toolModals,
    selection: state => state.selection,
    isFileSelected: state => file => !!state.selection?.find(item => item.id === file.id),
    getField: state => field => state.fields[field] ?? null,
}

export default getters
