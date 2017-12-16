import Inferno from 'inferno'

const FormatChoice = (props) => {
    const {value, name, dimensions, price, handleClick, checked } = props
    const iconSizeBase = 64
    const iconSize = window.innerHeight > 991 ? iconSizeBase * 2 : iconSizeBase
    return <label>
        <input type="radio" value={value} onClick={handleClick} checked={checked} />
        <svg height={iconSize} viewBox="0 0 24 24" width={iconSize}>
            <path d="M19 12h-2v3h-3v2h5v-5zM7 9h3V7H5v5h2V9zm14-6H3c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h18c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16.01H3V4.99h18v14.02z"/>
            <path d="M0 0h24v24H0z" fill="none"/>
        </svg><br/>
        <span>{name}</span><br/>
        <span>{dimensions}</span><br/>
        <span>{price}€</span>
    </label>
}

export default FormatChoice