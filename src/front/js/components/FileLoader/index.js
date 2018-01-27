import Inferno from 'inferno'

const FileLoader = (props) => {
    const { imgUrl, onChange, error, value } = props
    return <label>
        <div style={
            `
            background-image: url("${imgUrl}");
            background-size: contain;
            background-repeat: no-repeat;
            background-position: center;
            `
        }
        >
            <input
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