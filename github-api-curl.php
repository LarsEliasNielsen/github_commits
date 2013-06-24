<html>
<head>
  <title>GitHub Commit module</title>
  <link rel="stylesheet" href="css/main.style.css" />
</head>
<body>
  <?php
    /**
     * Gets the API (v3) from GitHub.com with cURL.
     *
     * @param string sshCloneUrl
     *  Copy the SSH Clone URL from GitHub
     * @param int numberOfCommits
     *  Number of commits you want to be shown
     * @return object result
     *  A JSON-object is returned from the API
     * 
     */
    function getGitHubApi($sshCloneUrl = 'git@github.com:LarsEliasNielsen/GitTest.git', $numberOfCommits = 5) {

      // $string = 'git@github.com:LarsEliasNielsen/GitTest.git';
      // echo 'string: '.$string.'<br />';

      // Regex to filter the git clone url
      $userPattern = array('/^git@github.com:/', '/\/[A-Za-z0-9\_\-]+.git$/');
      $repoPattern = array('/^git@github.com:[A-Za-z0-9\_\-]+\//', '/.git$/');

      // User and repo from clone url
      $user = preg_replace($userPattern, '', $sshCloneUrl);
      $repo = preg_replace($repoPattern, '', $sshCloneUrl);

      $curl = curl_init();
      curl_setopt_array($curl, array(
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_URL => 'https://api.github.com/repos/'.$user.'/'.$repo.'/commits',
        CURLOPT_USERAGENT => $numberOfCommits.' latest commits',
        // CURLOPT_USERPWD => 'USER:PASS'
      ));
      
      $result = curl_exec($curl);

      printCommits($result, $numberOfCommits);

      curl_close($curl);

    }

    /**
     *
     * Decodes and printes commit information
     *
     * @param object jsonResult
     *  JSON object returned from getGitHubApi()
     * @param int numberOfCommits
     *  Number of commits you want to be shown
     *
     */
    function printCommits($jsonResult, $numberOfCommits = 5) {
      $json = json_decode($jsonResult, true);

      for ($i = 0; $i < $numberOfCommits; $i ++) {

        $committerAvatar = $json[$i]['author']['avatar_url'];
        $committerUsername = $json[$i]['author']['login'];
        $committerUrl = $json[$i]['author']['html_url'];
        $commitMessage = $json[$i]['commit']['message'];

        $commitRawDate = $json[$i]['commit']['author']['date'];
        $commitDate = date('d-m-Y H:i:s', strtotime($commitRawDate));

        $commitLink = $json[$i]['html_url'];

        echo '<div class="gitCommit">
          <div class="committerImage"><a href="'.$committerUrl.'"><img src="'.$committerAvatar.'" title="'.$committerUsername.'" /></a></div>
          <div class="gitDetails">
            <div class="commitMessage"><a href="'.$commitLink.'">'.$commitMessage.'</a></div>
            <div class="commitAuthor">Authored on '.$commitDate.' by <a href="'.$committerUrl.'">'.$committerUsername.'</a></div>
          </div>
          <div class="commitLink"><a href="'.$commitLink.'">Browse commit</a></div>
        </div>';
      }
    }

  ?>

  <?php
    
    $returnedJSON = getGitHubApi('git@github.com:LarsEliasNielsen/GitHub-Commits.git', 5);

  ?>
</body>
</html>