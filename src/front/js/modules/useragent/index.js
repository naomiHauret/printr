import Useragent from '@cerebral/useragent'

const useragent = Useragent({

  media: {
    xs: '(min-width: 530px)',
    sm: '(min-width: 768px)',
    md: '(min-width: 992px)',
    lg: '(min-width: 1199px)',
    xl: '(min-width: 1441px)',
  },

  // update window size on resize
  window: true
})

export default  useragent