using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;

namespace Quantum.Tala.Service.Authentication
{
    public interface IUser
    {
        string Username { get; set; }
        string Password { get; set; }
        string Authkey { get; set; }
        int Money { get; set; }
    }
}
