import Inferno from 'inferno'
import styles from "./styles.css"

const FileLoader = (props) => {
    const { imgUrl, onChange, error, value, formatId } = props
    let size = 0
    let i = 2
    let id = 0
    formatId !== null ? id = formatId : id = i

    while (i < id ) {
        size++
        i++
    }
    const paperSize = styles[`sheet${size}`]

    return <label className="flex flex-col w-full justify-center items-center cursor-pointer">
        <div style={`background-image: url("${imgUrl}");`}
            className={`relative transition bg-white shadow-md bg-contain bg-no-repeat bg-center mb-6 ${styles.sheetShadowHover }`}
            data-flag="sheet"
        >
            <input
                className={`transition block invisible ${paperSize} `}
                type="file"
                accept="svg|png|pdf"
                onChange={onChange}
                value={value}
                required
            />
        </div>
        <small>Accepted formats: SVG, PNG, PDF.</small>
        <small>Your file must be <b>less than 2MB</b></small>
        {error && <div>
            {error}
        </div>}
    </label>
}

export default FileLoader