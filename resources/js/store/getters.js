const getters = {
    active: state => (state.toolModals.length > 0 ? state.toolModals[0] : null),
    allModals: state => state.toolModals,
    selection: state => {
        if (state.isFieldMode) {
            return state.currentField?.selection
        }

        return state.toolSelection
    },
    limit: state => {
        if (state.isFieldMode) {
            return state.currentField?.limit
        }

        return null
    },
    isFileSelected: state => file => {
        if (state.isFieldMode) {
            return !!state.currentField?.selection.find(item => item.id === file.id)
        }

        return !!state.toolSelection?.find(item => item.id === file.id)
    },
    fieldByAttribute: state => attribute => state.fields[attribute],
}

export default getters
