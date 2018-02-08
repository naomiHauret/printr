import Inferno from "inferno";

const QuantityInput = props => {
  const { onInput, value } = props

  return <label for="quantity" className="flex h-inherit">
      <b className="flex uppercase tracking-wide items-center justify-center text-grey-dark">
        Quantity
      </b>
      <input className="ml-2 h-full border-0  text-center text-xl w-24 bg-grey-lighter" placeholder="10..." type="number" min="1" onInput={onInput} value={value} id="quantity" name="quantity" required />
    </label>;
};

export default QuantityInput
