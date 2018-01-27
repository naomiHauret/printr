import Inferno from "inferno";

const QuantityInput = props => {
  const { onInput, value } = props

  return <label for="quantity">
      Quantity
      <input type="number" min="1" onInput={onInput} value={value} id="quantity" name="quantity" required />
    </label>;
};

export default QuantityInput
