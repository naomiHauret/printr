import Inferno from 'inferno'

const LoadingButton = (props) => {
    const {label, isLoadingData, isDisabled, onClick, classes} = props
    return <button disabled={isDisabled || isLoadingData} onClick={onClick} className={`${classes} ${isDisabled === false ? "cursor-pointer text-grey-dark" : "cursor-not-allowed"} ${isLoadingData ? "bg-grey-lighter": "bg-white"} } `}>
        <div className={`transition inline-block w-3 h-3 mr-3  rounded-full ${isDisabled === false ? "bg-gradient-green shadow-green" : "bg-gradient-orange shadow-orange"}`} />
        {isLoadingData ? "..." : label}
      </button>;
}

export default LoadingButton