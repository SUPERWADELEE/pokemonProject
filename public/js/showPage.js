function showPokemonPage() {
    // 隱藏登錄界面
    document.getElementById('loginPage').style.display = 'none';

    // 顯示寶可夢的界面
    document.getElementById('pokemonContainer').style.display = 'block';
  }



  function showLoginPage() {

    // 顯示登錄界面
    document.getElementById('loginPage').style.display = 'block';

    // 影藏寶可夢的界面
    document.getElementById('pokemonContainer').style.display = 'none';
  }