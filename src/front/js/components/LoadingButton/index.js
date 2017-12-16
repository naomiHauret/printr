import Inferno from 'inferno'

const LoadingButton = (props) => {
    const {label, isLoadingData, isDisabled, onClick} = props
    return <button
        disabled={
            isDisabled
            || isLoadingData
        }
        onClick={onClick}
    >
        {isLoadingData ? "..." : label }        
    </button>
}

export default LoadingButton