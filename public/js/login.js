function handleLogin() {
    const email = document.getElementById('emailInput').value;
    const password = document.getElementById('passwordInput').value;

    login(email, password)
      .then(token => {
        // 保存token到localStorage
        localStorage.setItem('jwtToken', token);

        // 顯示寶可夢的界面
        showPokemonPage();
      })
      .catch(error => {
        console.error('Login error:', error.message);
      });
  }

  function login(email, password) {
    return fetch('http://localhost:8000/api/Auth/login', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({
          email: email,
          password: password
        })
      })
      .then(response => {
        if (response.ok) {
          return response.json();
        } else {
          return response.json().then(data => {
            throw new Error(data.message || 'Unable to login');
          });
        }
      })
      .then(data => {
        return data.token;
      });
  }