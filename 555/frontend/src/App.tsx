import { useState } from "react";
import { BrowserRouter, Routes, Route } from "react-router-dom";
import Index from "./pages/Index";
import NotFound from "./pages/NotFound";
import Auth from "./pages/Auth";
import DriverBusSelection from "./pages/DriverBusSelection";

const App = () => {
  const [isAuthenticated, setIsAuthenticated] = useState(
    localStorage.getItem("isAuthenticated") === "true"
  );
  const [isDriver, setIsDriver] = useState(
    localStorage.getItem("isDriver") === "true"
  );
  const [hasSelectedBus, setHasSelectedBus] = useState(false);

  const handleSignup = async (email: string, password: string) => {

    const request = `http://localhost/login/signup.php?email=${email}&password=${password}`;
      fetch(request, { // Change this to your actual PHP script path
        method: 'GET',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded'
        },
      })
      .then(response => response.text())
      .then(data => {
        if (data === "1") {
          localStorage.setItem("isAuthenticated", "true");
          setIsAuthenticated(true);
          localStorage.setItem("isDriver", "False");
        }
      })
      .catch(error => {});
  };

  const handleLogin = async (email: string, password: string) => {
    const request = `http://localhost/login?email=${email}&password=${password}`;
    fetch(request, {
      method: "GET",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded",
      },
    })
      .then((response) => response.text())
      .then((data) => {
        if (data === "1") {
          localStorage.setItem("isDriver", "false");
          localStorage.setItem("isAuthenticated", "true");
          setIsAuthenticated(true);
          setIsDriver(false);
        } else if (data === "4") {
          localStorage.setItem("isDriver", "true");
          localStorage.setItem("isAuthenticated", "true");
          setIsAuthenticated(true);
          setIsDriver(true);
        }
      })
      .catch((error) => {});
  };

  const handleLogout = () => {
    localStorage.removeItem("isAuthenticated");
    localStorage.removeItem("isDriver");
    setIsAuthenticated(false);
    setIsDriver(false);
    setHasSelectedBus(false);
  };

  return (
    <BrowserRouter>
      <Routes>
        <Route
          path="/"
          element={
            isAuthenticated ? (
              isDriver && !hasSelectedBus ? (
                <DriverBusSelection onBusSelect={() => setHasSelectedBus(true)} />
              ) : (
                <Index onLogout={handleLogout} />
              )
            ) : (
              <Auth onLogin={handleLogin} onSignUp={handleSignup} />
            )
          }
        />
        <Route path="*" element={<NotFound />} />
      </Routes>
    </BrowserRouter>
  );
};

export default App;
