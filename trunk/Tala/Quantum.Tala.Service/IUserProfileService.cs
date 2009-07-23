using System;
namespace Quantum.Tala.Service
{
    public interface IUserProfileService
    {
        Quantum.Tala.Service.DTO.user_statDTO GetUserPlayStat(string p_sUsername);
    }
}
