const container = {
  width: '100%',
  maxWidth: '400px',
  padding: '0 20px',
  boxSizing: 'border-box',
}

const header = {
  textAlign: 'center',
  color: '#fff',
}

function FlatLogin() {
  return (
    <div style={container} className="dark">
      <div class="card">
        <h2 style={header}>Login</h2>
        <form>
          <input type="text" id="username" name="username" placeholder="Username" required />
          <input type="password" id="password" name="password" placeholder="Password" required />
          <button type="submit">Login</button>
        </form>
      </div>
    </div>
  )
}

export default FlatLogin
