import { useEffect, useState } from 'react'
import './App.css'

const API_URL = (import.meta.env.VITE_API_URL || 'http://127.0.0.1:8000/api').replace(/\/$/, '')
const emptyProduct = { product_name: '', description: '', price: '', quantity: '' }

function readStoredUser() {
  try {
    const rawUser = sessionStorage.getItem('user')
    return rawUser ? JSON.parse(rawUser) : null
  } catch {
    sessionStorage.removeItem('user')
    return null
  }
}

async function request(path, { token, ...options } = {}) {
  try {
    const response = await fetch(`${API_URL}${path}`, {
      ...options,
      headers: {
        ...(options.body ? { 'Content-Type': 'application/json' } : {}),
        ...(token ? { Authorization: `Bearer ${token}` } : {}),
        ...options.headers,
      },
    })

    const rawText = await response.text()
    let data = {}

    try {
      data = rawText ? JSON.parse(rawText) : {}
    } catch {
      data = {}
    }

    if (!response.ok) {
      const message = data.error || data.message || 'Request failed. Check the API connection and try again.'
      throw new Error(message)
    }

    return data
  } catch (error) {
    if (error instanceof TypeError || error instanceof Error && /fetch|Failed to fetch/i.test(error.message)) {
      throw new Error(`Unable to reach the API at ${API_URL}. Start the PHP backend or update VITE_API_URL.`)
    }
    throw error
  }
}

function App() {
  const [token, setToken] = useState(() => sessionStorage.getItem('access_token') || '')
  const [user, setUser] = useState(() => readStoredUser())
  const [products, setProducts] = useState([])
  const [login, setLogin] = useState({ email: '', password: '' })
  const [editing, setEditing] = useState(null)
  const [productForm, setProductForm] = useState(emptyProduct)
  const [busy, setBusy] = useState(false)
  const [error, setError] = useState('')
  const [notice, setNotice] = useState('')

  async function loadProducts(activeToken = token) {
    const data = await request('/products', { token: activeToken })
    setProducts(data.products || [])
  }

  useEffect(() => {
    if (!token) return
    let active = true
    request('/products', { token }).then((data) => {
      if (active) setProducts(data.products || [])
    }).catch((problem) => {
      if (!active) return
      setError(problem.message)
      if (problem.message === 'Unauthorized') {
        sessionStorage.removeItem('access_token')
        sessionStorage.removeItem('refresh_token')
        sessionStorage.removeItem('user')
        setToken('')
        setUser(null)
        setProducts([])
      }
    })
    return () => { active = false }
  }, [token])

  useEffect(() => {
    if (!notice) return undefined
    const timer = window.setTimeout(() => setNotice(''), 2800)
    return () => window.clearTimeout(timer)
  }, [notice])

  async function signIn(event) {
    event.preventDefault()
    setBusy(true)
    setError('')
    try {
      const data = await request('/auth/login', { method: 'POST', body: JSON.stringify(login) })
      sessionStorage.setItem('access_token', data.tokens.access_token)
      sessionStorage.setItem('refresh_token', data.tokens.refresh_token)
      sessionStorage.setItem('user', JSON.stringify(data.user))
      setUser(data.user)
      setToken(data.tokens.access_token)
      setNotice('Welcome back.')
    } catch (problem) {
      setError(problem.message)
    } finally {
      setBusy(false)
    }
  }

  async function signOut(notify = true) {
    const refreshToken = sessionStorage.getItem('refresh_token')
    if (notify && token) {
      try {
        await request('/auth/logout', { method: 'POST', token, body: JSON.stringify({ refresh_token: refreshToken }) })
      } catch {
        // Clear the browser session even if the API is temporarily unavailable.
      }
    }
    sessionStorage.removeItem('access_token')
    sessionStorage.removeItem('refresh_token')
    sessionStorage.removeItem('user')
    setToken('')
    setUser(null)
    setProducts([])
    setError('')
  }

  function beginCreate() {
    setEditing('new')
    setProductForm(emptyProduct)
    setError('')
  }

  function beginEdit(product) {
    setEditing(product.id)
    setProductForm({
      product_name: product.product_name || '',
      description: product.description || '',
      price: product.price || '',
      quantity: product.quantity ?? '',
    })
    setError('')
  }

  async function saveProduct(event) {
    event.preventDefault()
    setBusy(true)
    setError('')
    try {
      const isNew = editing === 'new'
      await request(isNew ? '/products' : `/products/${editing}`, {
        method: isNew ? 'POST' : 'PUT',
        token,
        body: JSON.stringify({ ...productForm, price: Number(productForm.price), quantity: Number(productForm.quantity) }),
      })
      await loadProducts()
      setEditing(null)
      setNotice(isNew ? 'Product added.' : 'Product updated.')
    } catch (problem) {
      setError(problem.message)
    } finally {
      setBusy(false)
    }
  }

  async function deleteProduct(product) {
    if (!window.confirm(`Delete ${product.product_name}? This cannot be undone.`)) return
    setError('')
    try {
      await request(`/products/${product.id}`, { method: 'DELETE', token })
      await loadProducts()
      setNotice('Product deleted.')
    } catch (problem) {
      setError(problem.message)
    }
  }

  if (!token) {
    return (
      <main className="login-layout">
        <section className="login-panel">
          <div className="login-card">
            <div className="brand login-brand"><span className="brand-mark" aria-hidden="true"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2.5 4.5 6.3v11.4L12 21.5l7.5-3.8V6.3L12 2.5Zm5.5 4.8-5.5 2.8-5.5-2.8L12 4.2l5.5 3.1Zm-11 3.4 4.5 2.3v5.6l-4.5-2.3V10.7Zm6 8 4.5-2.3v-5.6l4.5 2.3v5.6Z" fill="currentColor"/></svg></span><span className="brand-name"><span className="brand-lava">LAVA</span><span className="brand-lust">LUST</span></span></div>
            <h2>Welcome back</h2>
            <p className="muted">Use your product account to continue.</p>
            {error && <div className="alert" role="alert">{error}</div>}
            <form onSubmit={signIn} className="form-stack">
              <label>Email address<input type="email" autoComplete="username" required value={login.email} onChange={(event) => setLogin({ ...login, email: event.target.value })} placeholder="you@company.com" /></label>
              <label>Password<input type="password" autoComplete="current-password" required value={login.password} onChange={(event) => setLogin({ ...login, password: event.target.value })} placeholder="Your password" /></label>
              <button className="button button-dark button-wide" disabled={busy}>{busy ? 'Signing in...' : 'Sign in'} <span aria-hidden="true">→</span></button>
            </form>
          </div>
        </section>
      </main>
    )
  }

  const totalUnits = products.reduce((total, product) => total + Number(product.quantity || 0), 0)
  const inventoryValue = products.reduce((total, product) => total + Number(product.price || 0) * Number(product.quantity || 0), 0)
  const formatPeso = (value) => `₱${Number(value).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`

  return (
    <main className="app-shell">
      <header className="topbar">
        <a className="brand" href="#top"><span className="brand-mark" aria-hidden="true"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2.5 4.5 6.3v11.4L12 21.5l7.5-3.8V6.3L12 2.5Zm5.5 4.8-5.5 2.8-5.5-2.8L12 4.2l5.5 3.1Zm-11 3.4 4.5 2.3v5.6l-4.5-2.3V10.7Zm6 8 4.5-2.3v-5.6l-4.5 2.3v5.6Z" fill="currentColor"/></svg></span><span className="brand-name"><span className="brand-lava">LAVA</span><span className="brand-lust">LUST</span></span></a>
        <div className="topbar-right"><span className="user-chip">{user?.username || user?.email}</span><button className="button button-quiet" onClick={() => signOut()}>Log out <span aria-hidden="true">↗</span></button></div>
      </header>
      <section className="workspace" id="top">
        <div className="page-heading">
          <div><h1>Products <span className="heading-count">{String(products.length).padStart(2, '0')}</span></h1><p className="muted">A live view of everything in your catalog.</p></div>
          <button className="button button-dark" onClick={beginCreate}><span aria-hidden="true">＋</span> Add product</button>
        </div>
        {error && <div className="alert page-alert" role="alert">{error}<button onClick={() => setError('')} aria-label="Dismiss">×</button></div>}
        {notice && <div className="notice" role="status">{notice}</div>}
        <div className="metric-strip">
          <div className="metric"><span>CATALOG ITEMS</span><strong>{products.length}</strong></div>
          <div className="metric"><span>UNITS IN STOCK</span><strong>{totalUnits.toLocaleString()}</strong></div>
          <div className="metric"><span>INVENTORY VALUE</span><strong>{formatPeso(inventoryValue)}</strong></div>
          <div className="metric metric-date"><span>WORKSPACE</span><strong>{new Intl.DateTimeFormat('en', { month: 'short', day: '2-digit', year: 'numeric' }).format(new Date())}</strong></div>
        </div>
        <div className="catalog-heading"><div><p className="eyebrow">ALL PRODUCTS</p><h2>Catalog</h2></div><span className="catalog-meta">{products.length} {products.length === 1 ? 'RECORD' : 'RECORDS'}</span></div>
        <div className="table-wrap"><table>
          <thead><tr><th>PRODUCT</th><th>DESCRIPTION</th><th>PRICE</th><th>QUANTITY</th><th>CREATED</th><th><span className="sr-only">Actions</span></th></tr></thead>
          <tbody>
            {products.map((product, index) => (
              <tr key={product.id} style={{ animationDelay: `${index * 35}ms` }}>
                <td><div className="product-name"><span className="product-index">{String(index + 1).padStart(2, '0')}</span><strong>{product.product_name}</strong></div></td>
                <td className="description-cell">{product.description || '—'}</td><td className="price-cell">{formatPeso(product.price)}</td>
                <td><span className={`quantity ${Number(product.quantity) < 5 ? 'low-stock' : ''}`}>{product.quantity} <small>units</small></span></td>
                <td className="date-cell">{product.created_at ? new Date(product.created_at).toLocaleDateString() : '—'}</td>
                <td><div className="row-actions"><button className="icon-button" onClick={() => beginEdit(product)} aria-label={`Edit ${product.product_name}`}>Edit</button><button className="icon-button danger-action" onClick={() => deleteProduct(product)} aria-label={`Delete ${product.product_name}`}>Delete</button></div></td>
              </tr>
            ))}
+            {products.length === 0 && <tr><td className="empty-state" colSpan="6"><span className="empty-mark">∅</span><strong>Your catalog is clear.</strong><span>Add a product to start tracking inventory.</span><button className="button button-outline" onClick={beginCreate}>Add first product</button></td></tr>}
          </tbody>
        </table></div>
        <footer className="table-footer"><span><i className="status-dot" /> SYNCED WITH PRODUCT_DB</span><span>CHANGES SAVE TO THE API</span></footer>
      </section>
      {editing !== null && <div className="modal-backdrop" role="presentation" onMouseDown={(event) => { if (event.target === event.currentTarget) setEditing(null) }}>
        <section className="product-modal" role="dialog" aria-modal="true" aria-labelledby="modal-title">
          <div className="modal-heading"><div><p className="eyebrow">{editing === 'new' ? 'NEW RECORD' : `PRODUCT #${editing}`}</p><h2 id="modal-title">{editing === 'new' ? 'Add product' : 'Edit product'}</h2></div><button className="close-button" onClick={() => setEditing(null)} aria-label="Close">×</button></div>
          <form className="form-stack" onSubmit={saveProduct}>
            <label>Product name<input maxLength="100" required autoFocus value={productForm.product_name} onChange={(event) => setProductForm({ ...productForm, product_name: event.target.value })} placeholder="e.g. Field notebook" /></label>
            <label>Description<textarea rows="3" value={productForm.description} onChange={(event) => setProductForm({ ...productForm, description: event.target.value })} placeholder="A short product description" /></label>
            <div className="form-row"><label>Price<input type="number" min="0" step="0.01" required value={productForm.price} onChange={(event) => setProductForm({ ...productForm, price: event.target.value })} placeholder="0.00" /></label><label>Quantity<input type="number" min="0" step="1" required value={productForm.quantity} onChange={(event) => setProductForm({ ...productForm, quantity: event.target.value })} placeholder="0" /></label></div>
            {error && <div className="alert" role="alert">{error}</div>}
            <div className="modal-actions"><button type="button" className="button button-outline" onClick={() => setEditing(null)}>Cancel</button><button className="button button-dark" disabled={busy}>{busy ? 'Saving...' : editing === 'new' ? 'Create product' : 'Save changes'}</button></div>
          </form>
        </section>
      </div>}
    </main>
  )
}

export default App
